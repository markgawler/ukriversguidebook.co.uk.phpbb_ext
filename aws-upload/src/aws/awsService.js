import { config, S3, CognitoIdentityCredentials } from 'aws-sdk'

export default class AwsService {
  configure (appConfig) {
    const bucket = 'ukrgb-test-upload'
    const region = 'eu-west-1'
    const identityPoolId = 'eu-west-1:a79d444a-bb3a-4862-9ee2-ae776b0f3e6e'

    this.awsRegion = region
    this.albumBucketName = bucket
    this.baseUrl = 'https://s3-eu-west-1.amazonaws.com/' + bucket + '/'

    config.region = region // Region
    config.credentials = new CognitoIdentityCredentials({
      IdentityPoolId: identityPoolId
    })

    this.s3 = new S3({
      apiVersion: '2006-03-01',
      params: { Bucket: bucket }
    })
  }
  uploadFiles (formData, folder) {
    const photos = formData.getAll('photos')
    // Upload a list of files to an S3 bucket
    return this.uploadBatch(photos, folder)
    .then(() => photos)
  }

  uploadBatch (photos, folder) {
    return Promise.all(photos.map((file) => {
      const fn = encodeURIComponent(folder) + '/' + this.createFileName(file.name)
      const params = {
        Key: fn,
        Body: file,
        ACL: 'public-read'
      }
      file.url = this.baseUrl + fn
      return this.s3.upload(params).promise()
    }))
  }

  createFolder (name) {
    const albumKey = encodeURIComponent(name) + '/'
    return this.s3.headObject({Key: albumKey}).promise()
    .then((data) => {
      // Folder exists
      return 'Success'
    })
    .catch((err) => {
      if (err.code === 'NotFound') {
        // Folder not found, create it
        return this.s3.putObject({Key: albumKey}).promise()
        .then((data) => {
          return 'Success'
        })
        .catch((err) => {
          return 'Error creating folder: ' + err.message
        })
      }
      return 'Unspecified error'
    })
  }

  createFileName (fileName) {
    return encodeURIComponent(Date.now() + '-' + fileName)
  }

  dataURItoBlob (dataURI) {
    var binary = atob(dataURI.split(',')[1])
    var array = []
    for (var i = 0; i < binary.length; i++) {
      array.push(binary.charCodeAt(i))
    }
    return new Blob([new Uint8Array(array)], {type: 'image/png'})
  }

  uploadDataUri (dataURI, name, folder) {
    const basename = name.substr(0, name.lastIndexOf('.'))
    const key = encodeURIComponent(folder) + '/' + this.createFileName(basename) + '.png'
    const blob = this.dataURItoBlob(dataURI)
    const params = {
      Key: key,
      Body: blob,
      ACL: 'public-read',
      ContentType: 'image/png'
    }
    return this.s3.upload(params).promise()
  }
}

AwsService.install = function (Vue, options) {
  Object.defineProperty(Vue.prototype, '$awsService', {
    get () { return this.$root._awsService }
  })

  Vue.mixin({
    beforeCreate () {
      if (this.$options.awsService) {
        this._awsService = this.$options.awsService
        this._awsService.configure(options)
      }
    }
  })
}
