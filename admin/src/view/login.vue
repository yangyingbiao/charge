<template>
    <div class='login-bc'>
        <div class='fixed xy login-container'>
            <div class='login-form'>
                <div class='f-18 font-bold'>登录</div>
                <el-form class='m-t-30' ref='form' :model='formData' :rules='rules'>
                    <el-form-item prop='account'>
                        <el-input type='text' clearable placeholder='登录账号' v-model='formData.account' @keyup.enter.native='keyup' />
                    </el-form-item>
                    <el-form-item prop='password'>
                        <el-input type='password' clearable show-password placeholder='登录密码' v-model='formData.password' @keyup.enter.native='keyup' />
                    </el-form-item>
                </el-form>

                <div class='m-t-20'>
                    <el-button class='w-100' type='primary' @click='login' :loading='loading'>{{loading ? '登录中' : '登录'}}</el-button>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import { Http } from '@/utils'
    import { getMenu } from '@/router'
    let http = new Http()
    export default {
        data () {
            return {
                loading : false,
                formData : {
                    account : '',
                    password : ''
                },

                rules : {
                    account : {
                        required : true,
                        message: '请输入登录账号'
                    },

                    password : {
                        required : true,
                        message: '请输入登录密码'
                    }
                }
            }
        },

        methods : {
            keyup (e) {
                if(e.keyCode == 13) this.login()
            },
            async login () {
                this.$refs.form.validate(async (valid) => {
                    if(!valid) return
                    this.loading = true
                    let res = await http.post('login', this.formData)
                    this.loading = false
                    if(res.success) {
                        let data = res.data
                        if(data.user.permission) {
                            this.$store.commit('updateUserPermission', data.user.permission)
                            delete data.user.permission
                        }
                        this.$store.commit('updateUserInfo', data.user)
                        this.$store.commit('updateLoginToken', data.token)
                        this.$store.commit('updateRefreshToken', data.refreshToken)
                        this.$store.commit('updateLoginRefreshKey', data.key)
                        this.$store.commit('updateLoginExpire', data.expire || 7200)

                        this.$router.replace('/').catch(e => {
                            console.log(e.message)
                        })
                    }else {
                        this.errorToast(res.msg)
                    }
                })
            }
        }
    }
</script>

<style scoped lang='scss'>
    .login-bc{
        height: 100%;
    }

    .login-container{
        width: 500px;
        box-shadow : rgba(0, 0, 0, 0.08) 0px 0px 4px 0px;
    }

    .login-form{
        width: 70%;
        margin: auto;
        padding: 30px 0;
    }
</style>