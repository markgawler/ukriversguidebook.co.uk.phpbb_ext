import { config, S3, CognitoIdentityCredentials } from 'aws-sdk'

const albumBucketName = 'ukrgb-test-upload'
const BaseUrl = 'https://s3-eu-west-1.amazonaws.com/' + albumBucketName + '/'

export default class AwsService {
  configure (appConfig) {
    const awsRegion = 'eu-west-1'
    const IdentityPoolId = 'eu-west-1:a79d444a-bb3a-4862-9ee2-ae776b0f3e6e'

    config.region = awsRegion // Region
    config.credentials = new CognitoIdentityCredentials({
      IdentityPoolId: IdentityPoolId
    })

    this.s3 = new S3({
      apiVersion: '2006-03-01',
      params: { Bucket: albumBucketName }
    })
  }

  uploadFiles (formData) {
    const photos = formData.getAll('photos')

    // Upload a list of files to an S3 bucket
    return this.uploadBatch(photos)
    .then(() => photos)
  }

  uploadBatch (photos) {
    return Promise.all(photos.map((file) => {
      const params = {
        Key: file.name,
        Body: file,
        ACL: 'public-read'
      }
      console.log(file)
      file.url = BaseUrl + file.name
      return this.s3.upload(params).promise()
    }))
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
