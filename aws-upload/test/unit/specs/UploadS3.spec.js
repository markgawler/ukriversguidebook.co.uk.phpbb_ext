import Vue from 'vue'
import UploadS3 from '@/UploadS3'

describe('UploadS3.vue', () => {
  it('should render correct contents', () => {
    const Constructor = Vue.extend(UploadS3)
    const vm = new Constructor().$mount()
    expect(vm.$el.querySelector('h1').textContent)
    .toEqual('123456')
  })
})
