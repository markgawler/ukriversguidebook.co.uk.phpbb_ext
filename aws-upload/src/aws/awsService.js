import { config, S3, CognitoIdentityCredentials } from 'aws-sdk'

// const albumBucketName = 'ukrgb-test-upload'
// const BaseUrl = 'https://s3-eu-west-1.amazonaws.com/' + albumBucketName + '/'

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
  uploadFiles (formData) {
    const photos = formData.getAll('photos')
    // Upload a list of files to an S3 bucket
    return this.uploadBatch(photos)
    .then(() => (photos))
  }

  uploadBatch (photos) {
    return Promise.all(photos.map((file) => {
      const params = {
        Key: this.createFileName(file.name),
        Body: file,
        ACL: 'public-read'
      }
      // console.log(file)
      file.url = this.baseUrl + file.name
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
