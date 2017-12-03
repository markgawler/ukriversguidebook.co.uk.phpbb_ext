import Vue from 'vue'
import AwsService from './awsService'
import config from '@/config'

Vue.use(AwsService, config)

export default new AwsService()
