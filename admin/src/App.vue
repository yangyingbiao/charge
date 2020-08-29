<template>
  <div id='app' style='height:100%;'>
    <router-view />
  </div>
</template>

<script>
import { Http } from '@/utils'
let http = new Http()
export default {
  name: 'App',
  methods : {
    async refreshToken () {
      let token = this.$store.state.refreshToken
      if(!token) return

      let loginExpire = this.$store.state.loginExpire
      if(loginExpire == 0) return

      let time = new Date() * 1
      if((loginExpire - time) > (20 * 60 * 100)) return

      //距离到期时间小于或等于20分钟时就刷新
      let res = await http.post('login/refresh', {token : token, key : this.$store.state.loginRefreshKey})
      console.log('更新')
      console.log(res)
      if(res.success) { //更新成功
        let data = res.data
        this.$store.commit('updateLoginToken', data.token)
        this.$store.commit('updateRefreshToken', data.refreshToken)
        this.$store.commit('updateLoginRefreshKey', data.key)
        this.$store.commit('updateLoginExpire', data.expire || 7200)
      }
    }
  },

  created () {
    this.refreshToken()
    setInterval(() => {
      this.refreshToken()
    }, 5 * 60 * 1000)


    {
      //获取各种配置
      (new Http()).get('sys/conf').then(({data}) => {
        if(data) {
          this.$store.commit('updateSysConf', data)
        }
      })
    }

  }
}
</script>