import { Http } from '@/utils'
let http = new Http()

export default {
    data () {
        return {
            id : '',
            formData : {
                priceName : '',
                chargeType : 1,
                chargeUnit : '',
                maxPower : '',
                criticalPower : '',
                checkTime : '',
                prepayment : '',
                powerClass : [],
                options : []
            }
        }
    },

    methods : {
        addOption() {
            this.formData.options.push({amount : '', quantity : ''})
        },

        deleteOption(index) {
            this.formData.options.splice(index, 1)
        },

        addPowerClass() {
            this.formData.powerClass.push({quantity : '', power : ''})
        },

        deletePowerClass(index) {
            this.formData.powerClass.splice(index, 1)
        },
        
        async submit() {
            let formData = this.formData
            for(let k in formData) {
                if(formData[k] === '') {
                    this.errorToast('请填写完整数据')
                    return
                }
            }

            for(let i = 0; i < formData.powerClass.length; i ++) {
                if(formData.powerClass[i].quantity === '' || formData.powerClass[i].power === '') {
                    this.errorToast('请填写完整数据')
                    return
                }
            }

            for(let i = 0; i < formData.options.length; i ++) {
                if(formData.options[i].amount === '' || formData.options[i].quantity === '') {
                    this.errorToast('请填写完整数据')
                    return
                }
            }
            let url = 'chargePrice/add'
            if(this.id > 0) {
                formData.id = this.id
            }

            let res = formData.id ? await http.put('chargePrice/edit', formData) : await http.post('chargePrice/add', formData)
            if(res.success) {
                this.successToast('保存成功')
                this.$router.go(-1)
            }else {
                this.errorToast(res.msg)
            }

            
        }
    },

    created () {
        let id = this.$route.params.id
        if(id > 0) {
            (new Http()).get('chargePrice', {id}).then(({data}) => {
                if(!data) return
                if(data.options) {
                    if(typeof data.options === 'string') {
                        data.options = JSON.parse(data.options)
                    }
                }else {
                    data.options = []
                }

                if(data.powerClass) {
                    if(typeof data.powerClass === 'string') {
                        data.powerClass = JSON.parse(data.powerClass)
                    }
                }else {
                    data.powerClass = []
                }

                for(let k in this.formData) {
                    if(k in data) {
                        this.formData[k] = data[k] || ''
                    }
                }


            })

            this.id = id
        }
    },

    mounted () {
        
    }
}