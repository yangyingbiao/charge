module.exports = {
    productionSourceMap : false,
    lintOnSave : false,

    devServer : {
      proxy : {
        '/' : {
          target: 'http://charge.hundcang.com'
        }
      }
    }
  }