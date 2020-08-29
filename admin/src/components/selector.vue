<template>
    <div class='selector-container'>
        <span class='item' v-for='(item, index) in selectedValues' :key='index'>
            <el-select style='width:100%;' filterable clearable :size='size' v-model='selectedValues[index]' @change='selectOption($event, index)'>
                <template v-for='option in options[index]'>
                    <el-option :key='option[keyField]' :value='option[keyField]' :label='option[labelField]'></el-option>
                </template>
            </el-select>
        </span>
    </div>
</template>

<script>
import { Http } from '@/utils'
export default {
    name : 'selector',
    props : {
        size : {
            type : String,
            default : ''
        },

        fixedCount : {
            type : Boolean,
            default : true
        },

        count : {
            type : Number,
            default : 3
        },

        value : {
            type : Array,
            default () {
                return []
            }
        },

        url : {
            type : String,
            default : ''
        },

        parentName : {
            type : String,
            default : ''
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
            selectedValues : [],
            options : []
        }
    },

    watch : {
        value (value) {
            if(value.join(',') != this.selectedValues.join(',')) {
                this.restore()
            }
        }
    },

    methods : {
        async getOption(v = '', index) {
            if(this.fixedCount) {
                this.options[index].splice(0)
            }
            let http = new Http()
            let res = await http.get(this.url, {[this.parentName] : v})
            let data = res.data || []

            if(this.fixedCount) {
                data.forEach(item => {
                    this.options[index].push(item)
                })
            }else {
                if(data.length > 0) {
                    if(this.selectedValues[index] === undefined) {
                         this.$set(this.selectedValues, index, '')
                    }
                    this.$set(this.options, index, data)
                }
            }
        },

        selectOption(v, index) {
            if(this.fixedCount) {
                if(index < (this.count - 1)) {
                    for(let i = index + 1; i < this.count; i ++) {
                        this.selectedValues[i] = ''
                        this.options[i].splice(0)
                    }

                    if(v !== '') {
                        this.getOption(v, index + 1)
                    }
                }
            }else {
                this.selectedValues.splice(index + 1)
                this.options.splice(index + 1)
                if(v !== '') {
                    this.getOption(v, index + 1)
                }
            }
            

            this.$emit('input', this.selectedValues)
            this.$emit('change', this.selectedValues)
            
        },

        restore () {
            for(let i = 0; i < this.value.length; i ++) {
                this.$set(this.selectedValues, i, this.value[i])
                if(i < (this.value.length - 1)) {
                    this.getOption(this.value[i], i + 1)
                }
                
            }
        }
    },

    created () {
        if(this.fixedCount) { //固定数量
            for(let i = 0; i < this.count; i ++) {
                this.selectedValues.push('')
                this.options.push([])
            }
        }else {
            if(this.value.length > 0) {
                this.value.forEach(item => {
                    this.selectedValues.push('')
                })
            }else {
                this.selectedValues.push('')
                this.options.push([])
            }
            
        }

        this.restore()

        this.getOption('', 0)
        
    }

    
}
</script>

<style scoped lang='scss'>
    .selector-container{
        display: inline-flex;
        .item{
            flex: 1;
            margin-left: 10px;
            &:first-child{
                margin-left: 0;
            }
        }
    }
</style>