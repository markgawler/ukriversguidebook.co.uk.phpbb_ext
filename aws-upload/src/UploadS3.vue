<template>
  <div id="uploads3">
    <div v-if="isDevel">
      <textarea name="message" id="message" rows="15" cols="76" tabindex="4"  class="inputbox"></textarea>
    </div>
    <div class="container">
      <!--UPLOAD-->
      <form enctype="multipart/form-data" novalidate v-if="isInitial">
        <div class="dropbox">
            <input type="file" multiple :name="uploadFieldName" :disabled="isSaving" @change="filesChange($event.target.name, $event.target.files); fileCount = $event.target.files.length" accept="image/*" class="input-file">
          <p v-if="isInitial">
            Drag your file(s) here to begin uploading images<br> or click to browse
          </p>
        </div>
      </form>
      <!--Progress-->
      <div>
        <h2 v-if="isSaving">
          Uploading {{ uploadCount }} file of {{fileCount}}.
        </h2> 
        <h2 v-if="isResize">
          Resizing {{ fileCount }} files...
        </h2>
      </div>
      <!--SUCCESS-->
      <div v-if="isSuccess">
        <h2>Uploaded {{ uploadedFiles.length }} file(s) successfully.</h2>
        <p>
          <a href="javascript:void(0)" @click="reset()">Upload again</a>
        </p>
      </div>
      <!-- Preview-->
      <div v-if="isSuccess || isResize || isSaving">
        <ul class="list-unstyled">
          <li v-for="item in uploadedFiles">
            <img :src="item.url" class="img-preview" :alt="item.name">
          </li>
        </ul>
      </div>
      <!--FAILED-->
      <div v-if="isFailed">
        <div class="alert alert-block alert-error">
          <h2 class="alert-error">Upload failed.</h2>
          <p>{{ uploadError }}</p>
        </div>
        <p>
          <a class="alert-error" href="javascript:void(0)" @click="reset()">Try again</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script>

import appConfig from '@/config'

const STATUS_INITIAL = 0
const STATUS_SAVING = 1
const STATUS_SUCCESS = 2
const STATUS_FAILED = 3
const STATUS_RESIZE = 4

export default {
  name: 'UploadS3',
  data () {
    let userId = '0000'
    if (window.hasOwnProperty('phpbbUserId')) {
      userId = window.phpbbUserId
    }
    return {
      uploadFieldName: 'photos',
      currentStatus: null,
      uploadedFiles: [],
      uploadCount: this.uploadCount,
      userId: userId
    }
  },
  computed: {
    isInitial () {
      return this.currentStatus === STATUS_INITIAL
    },
    isSaving () {
      return this.currentStatus === STATUS_SAVING
    },
    isSuccess () {
      return this.currentStatus === STATUS_SUCCESS
    },
    isFailed () {
      return this.currentStatus === STATUS_FAILED
    },
    isResize () {
      return this.currentStatus === STATUS_RESIZE
    },
    isDevel () {
      console.log('isDevel called')
      return !window.hasOwnProperty('phpbbUserId')
    }
  },
  methods: {
    reset () {
      // reset form to initial state
      this.currentStatus = STATUS_INITIAL
      this.uploadedFiles = []
      this.uploadError = null
      this.uploadCount = 1 // Don't display uploding the zeroth file
      this.failedCount = 0
    },

    filesChange (fieldName, fileList) {
      // handle file changes
      const formData = new FormData()

      if (!fileList.length) return
      if (fileList.length > 4) {
        this.currentStatus = STATUS_FAILED
        this.uploadError = 'Error: A maximum of 4 images can be uploaded'
        return
      }
      // append the files to FormData
      Array
        .from(Array(fileList.length).keys())
        .map(x => {
          formData.append(fieldName, fileList[x], fileList[x].name)
        })

      this.resizeImages(formData)
      .then(files => {
        this.uploadedFiles = files
        this.upload()
      })
      .catch((err) => {
        this.currentStatus = STATUS_FAILED
        this.uploadError = err.message
      })
      this.currentStatus = STATUS_RESIZE
    },

    insertBBCode (imgUri) {
      const el = document.getElementById('message')
      const bbCode = '[img]https://' + appConfig.domain + '/' + imgUri + '[/img]\n'

      let text = el.value
      el.value = text + bbCode
    },

    upload () {
      this.currentStatus = STATUS_SAVING
      const folder = appConfig.basePath + '/' + encodeURIComponent(this.userId)
      this.$awsService.createFolder(folder)
      .then(() => {
        this.uploadedFiles.map(file => {
          this.$awsService.uploadDataUri(file.url, file.name, folder)
          .then((params) => {
            this.insertBBCode(params.key)
            this.uploadCount += 1
            if (this.uploadCount + this.failedCount > this.uploadedFiles.length) {
              this.currentStatus = STATUS_SUCCESS
            }
          })
          .catch((err) => {
            console.log('File upload Failed')
            console.log(err)
            this.failedCount += 1
            this.currentStatus = STATUS_FAILED
            this.uploadError = err.message
          })
        })
      })
      .catch((err) => {
        console.log('failed to create folder')
        this.currentStatus = STATUS_FAILED
        this.uploadError = err.message
      })
    },

    resizeImages (formData) {
      const photos = formData.getAll('photos')
      return this.resizeBatch(photos)
      .then(() => photos)
    },

    resizeBatch (photos) {
      return Promise.all(photos.map((file) => {
        if (!file.type.match(/image.*/)) {
          return Promise.reject(Error(file.name + ' is not an image file'))
        } else {
          const p = this.getImage(file)
          p.then((f) => (file.url = f))
          .catch((err) => (this.uploadError = err.message))
          return p
        }
      }))
    },

    getImage (file) {
      return new Promise((resolve, reject) => {
        const img = document.createElement('img')
        const reader = new FileReader()
        reader.onload = (e) => {
          img.src = e.target.result
          img.onload = (e) => resolve(this.getBase64Image(img))
          img.onerror = (e) => reject(Error('File cannot be resized: ' + file.name))
        }
        reader.readAsDataURL(file)
      })
    },

    getBase64Image (img) {
      const canvas = document.createElement('canvas')
      const imgDimensions = this.calcImageDimensions(img.width, img.height)
      canvas.width = imgDimensions.w
      canvas.height = imgDimensions.h
      const ctx = canvas.getContext('2d')
      ctx.drawImage(img, 0, 0, canvas.width, canvas.height)

      const dataURL = canvas.toDataURL('image/png')
      return dataURL
    },

    calcImageDimensions (width, height) {
      const maxWidth = appConfig.maxWidth
      const maxHeight = appConfig.maxHeight
      let imgWidth = width
      let imgHeight = height
      if (width > maxWidth || height > maxHeight) {
        const aspectRatio = width / height
        if (aspectRatio > 1) {
          imgWidth = maxWidth
          imgHeight = maxWidth / aspectRatio
        } else {
          imgHeight = maxHeight
          imgWidth = maxHeight * aspectRatio
        }
      }
      return {
        w: imgWidth,
        h: imgHeight}
    }
  },

  mounted () {
    this.reset()
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
  .dropbox {
    outline: 2px dashed grey; /* the dash box */
    outline-offset: -10px;
    background: lightcyan;
    color: dimgray;
    /* padding: 10px 10px; */
    min-height: 200px; /* minimum height */
    position: relative;
    cursor: pointer;
  }
  #uploads3 input[type="file"] {
    height: 200px;
    width: 100%;
  }
  
  .input-file {
    opacity: 0; /* invisible but it's there! */
    height: 200px;
    width: 100%;
    position: absolute;
    cursor: pointer;
    display: block;
  }
  
  .dropbox:hover {
    background: lightblue; /* when mouse over to the drop zone, change color */
  }
  
  .dropbox p {
    font-size: 1.2em;
    text-align: center;
    padding: 50px 0;
  }

  .img-preview {
    max-height : 100px;
    padding: 10px;
  }
 .list-unstyled {
    list-style-type: none;
  }
  #uploads3 li {
    display: inline;
  }

  #uploads3 h2.alert-error {
      color: #b94a48;
  }
</style>
