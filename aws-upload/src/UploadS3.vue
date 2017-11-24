<template>
  <div id="uploads3">
    <h1>{{msg}}</h1>
    <div class="container">
      <!--UPLOAD-->
      <form enctype="multipart/form-data" novalidate v-if="isInitial || isSaving">
        <div class="dropbox">
            <input type="file" multiple :name="uploadFieldName" :disabled="isSaving" @change="filesChange($event.target.name, $event.target.files); fileCount = $event.target.files.length" accept="image/*" class="input-file">
          <p v-if="isInitial">
            Drag your file(s) here to begin uploading images<br> or click to browse
          </p>
          <p v-if="isSaving">
            Uploading {{ fileCount }} files...
          </p>
        </div>
      </form>
      <!--SUCCESS-->
      <div v-if="isSuccess">
        <h2>Uploaded {{ uploadedFiles.length }} file(s) successfully.</h2>
        <p>
          <a href="javascript:void(0)" @click="reset()">Upload again</a>
        </p>
        <ul class="list-unstyled">
          <li v-for="item in uploadedFiles">
            <img :src="item.url" class="img-preview" :alt="item.name">
          </li>
        </ul>
      </div>
      <!--FAILED-->
      <div v-if="isFailed">
        <h2>Uploaded failed.</h2>
        <p>
          <a href="javascript:void(0)" @click="reset()">Try again</a>
        </p>
        <pre>{{ uploadError }}</pre>
      </div>
    </div>
  </div>
</template>

<script>

const STATUS_INITIAL = 0
const STATUS_SAVING = 1
const STATUS_SUCCESS = 2
const STATUS_FAILED = 3

export default {
  name: 'UploadS3',
  data () {
    if (!window.hasOwnProperty('phpbbUserId')) {
      window.phpbbUserId = 123456
    }
    return {
      msg: window.phpbbUserId,
      uploadFieldName: 'photos',
      currentStatus: null,
      uploadedFiles: []
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
    }
  },
  methods: {
    reset () {
      // reset form to initial state
      this.currentStatus = STATUS_INITIAL
      this.uploadedFiles = []
      this.uploadError = null
    },
    filesChange (fieldName, fileList) {
      // handle file changes
      const formData = new FormData()

      if (!fileList.length) return
      // append the files to FormData
      Array
        .from(Array(fileList.length).keys())
        .map(x => {
          formData.append(fieldName, fileList[x], fileList[x].name)
        })
      this.save(formData)
    },
    save (formData) {
      this.$awsService.createFolder(window.phpbbUserId)
      .then(
        this.$awsService.uploadFiles(formData, window.phpbbUserId)
        .then(files => {
          this.uploadedFiles = files
          files.map((file) => {
            console.log(file)
          })
          this.currentStatus = STATUS_SUCCESS
        })
        .catch((err) => {
          this.currentStatus = STATUS_FAILED
          this.uploadError = err.message
        }))
      .catch(() => {
        console.log('failed to create folder')
        this.currentStatus = STATUS_FAILED
      })
      this.currentStatus = STATUS_SAVING
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
  /* #phpbb input[type="file"] { */
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
</style>
