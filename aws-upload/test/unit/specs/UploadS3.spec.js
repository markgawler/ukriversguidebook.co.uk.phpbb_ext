import Vue from 'vue'
import UploadS3 from '@/UploadS3'

describe('UploadS3.vue', () => {
  const Constructor = Vue.extend(UploadS3)
  const vm = new Constructor().$mount()

  it('should render correct contents', () => {
    expect(vm.$el.querySelector('h1').textContent)
    .toEqual('123456')
  })
  it('Image Dimensions No change W:1024, H:768)', () => {
    expect(vm.calcImageDimensions(1024, 768)).toEqual({w: 1024, h: 768})
  })

  it('Image Dimensions No change W:100, H:100', () => {
    expect(vm.calcImageDimensions(100, 100)).toEqual({w: 100, h: 100})
  })

  it('Image fimensions Crop Width W:2048, h:768', () => {
    expect(vm.calcImageDimensions(2048, 768)).toEqual({w: 1024, h: 384})
  })

  it('Image fimensions Crop Height W:512, H:1536', () => {
    expect(vm.calcImageDimensions(512, 1536)).toEqual({w: 256, h: 768})
  })

  it('Image fimensions', () => {
    expect(vm.calcImageDimensions(2048, 1536)).toEqual({w: 1024, h: 768})
  })
})
