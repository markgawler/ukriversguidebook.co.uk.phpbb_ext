// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
// import App from './App'
import UploadS3 from '@/UploadS3'
import awsService from './aws'

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  el: '#uploads3',
  template: '<UploadS3/>',
  components: {
    UploadS3},
  awsService
})
