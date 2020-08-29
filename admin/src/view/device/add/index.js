import { Http } from '@/utils'
let http = new Http()

export default {
    data () {
        return {
            formData : {
                deviceType : [],
                deviceName : '',
                simIccid : '',
                portCount : '',
                networkType : '',
                priceT : '',
                priceW : '',
                area : [],
                address : '',
                status : true
            },

            rules : {
                deviceType : {
                    required : true,
                    validator : (rule, value, callback) => {
                        if(value.length == 0 || value[value.length - 1] == '') {
                            callback(new Error('请选择设备类型'))
                        }else {
                            callback()
                        }
                    }
                },

                simIccid : {
                    required : true,
                    message: '请输入SIM ICCID'
                }
            }
        }
    },

    methods : {
        submit() {
            this.$refs.form.validate((valid) => {
                if(!valid) return
                let formData = JSON.parse(JSON.stringify(this.formData))
                formData.status = Number(formData.status)
                formData.deviceType = formData.deviceType.pop()
                if(formData.id) {
                    http.put('device/edit', formData).then(res => {
                        if(res.rulesuccess) {
                            this.successToast('保存成功')
                        }else {
                            this.errorToast(res.msg)
                        }
                    })
                }else {
                    http.post('device/add', formData).then(res => {
                        if(res.rulesuccess) {
                            this.successToast('保存成功')
                        }else {
                            this.errorToast(res.msg)
                        }
                    })
                }
                
                
            })
        }
    },

    created () {
        let deviceId = this.$route.params.deviceId || 0
        if(deviceId > 0) {
            http.get('device/editInfo', {id : deviceId}).then(({ data }) => {
                if(data) {
                    for(let k in this.formData) {
                        if(k in data) {
                            this.formData[k] = data[k] || ''
                        }
                    }

                    this.formData.status = Boolean(this.formData.status)
                }
            })

            this.$set(this.formData, 'id', deviceId)
        }
    }
}