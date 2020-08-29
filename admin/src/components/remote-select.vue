<template>
    <el-select clearable filterable :size='size' v-model='selectedValue' @change='selectOption($event)'>
        <template v-for='option in options'>
            <el-option :key='option[keyName]' :value='option[keyName]' :label='option[labelName]'></el-option>
        </template>
    </el-select>
</template>

<script>
import { Http } from '@/utils'
export default {
    name : 'remote-select',
    props : {
        size : {
            type : String,
            default : ''
        },

        value : {
            type : [String, Number],
            default : ''
        },

        preset : {
            type : String,
            default :''
        },

        url : {
            type : String,
            default : ''
        },

        query : {
            type : Object,
            default () {
                return {}
            }
        },

        keyField : {
            type : String,
            default : 'id'
        },

        labelField : {
            type : String,
            default : 'name'
        }
    },

    data () {
        return {
            presetUrl : {networkType : {url : 'networkType/getType'}, 'chargePrice' : {url : 'chargePrice/getList', keyField : 'priceId', labelField : 'priceName'}},
            keyName : '',
            labelName : '',
            selectedValue : '',
            options : []
        }
    },

    watch : {
        value (value) {
            if(value !== this.selectedValue) {
                this.selectedValue = this.value
            }
        }
    },

    methods : {
        selectOption(v) {
            this.$emit('input', this.selectedValue)
            this.$emit('change', this.selectedValue)
            
        }
    },

    created () {
        this.selectedValue = this.value
        
        let url = this.url
        this.keyName = this.keyField
        this.labelName = this.labelField

        if(this.preset !== '') { //使用预设
            let preset = this.presetUrl[this.preset]
            url = preset.url

            if(preset.keyField) {
                this.keyName = preset.keyField
            }

            if(preset.labelField) {
                this.labelName = preset.labelField
            }
        }
        

        {
            (new Http()).get(url, this.query).then(res => {
                if(res.data) {
                    this.options = res.data
                }
            })
        }
    }

    
}
</script>