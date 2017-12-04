export default {
  region: process.env.AWS_REGION,
  bucket: process.env.AWS_S3_BUCKET,
  identityPoolId: process.env.AWS_COGNITO_IDENTITY_POOL_ID,
  baseUrl: process.env.BASE_URL,
  basePath: process.env.BASE_PATH,
  domain: process.env.DOMAIN,
  maxWidth: process.env.MAX_IMAGE_WIDTH,
  maxHeight: process.env.MAX_IMAGE_HEIGHT
}
