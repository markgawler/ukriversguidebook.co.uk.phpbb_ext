import AwsService from '@/aws/awsService'

describe('awsService.js', () => {
  const s = new AwsService()
  s.configure()

  it('create s3 bucket', () => {
    expect.assertions(1)
    return expect(s.createFolder('testFolder')).resolves.toEqual('Success')
  })

  it('create a unique filename', () => {
    const name = 'Hello'
    const regX = new RegExp(/^[0-9]{13}-.*/)
    const fn = s.createFileName(name)
    expect(fn).toMatch(regX)
    expect(fn.substring(14)).toEqual(name)
  })
})
