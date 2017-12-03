export default {
  bucket: process.env.AWS_S3_BUCKET,
  region: process.env.AWS_REGION,
  identityPoolId: process.env.AWS_COGNITO_IDENTITY_POOL_ID,
  baseUrl: process.env.BASE_URL,
  maxWidth: process.env.MAX_IMAGE_WIDTH,
  maxHeight: process.env.MAX_IMAGE_HEIGHT
}
