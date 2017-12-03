import { config, S3, CognitoIdentityCredentials } from 'aws-sdk'

export default class AwsService {
  configure (appConfig) {
    const identityPoolId = appConfig.identityPoolId
    const bucket = appConfig.bucket
    const region = appConfig.region

    this.baseUrl = appConfig.baseUrl + bucket + '/'

    config.region = region // Region
    config.credentials = new CognitoIdentityCredentials({
      IdentityPoolId: identityPoolId
    })

    this.s3 = new S3({
      apiVersion: '2006-03-01',
      params: { Bucket: bucket }
    })
  }

  createFolder (name) {
    const albumKey = encodeURIComponent(name) + '/'
    return this.s3.headObject({Key: albumKey}).promise()
    .catch((err) => {
      if (err.code === 'NotFound') {
        // Folder not found, create it
        return this.s3.putObject({Key: albumKey}).promise()
      }
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
