<template>
    <div style='font-size:0px;display:inline-block;'>
        <el-input clearable size='small' :placeholder='placeholder' class='input f-14' v-model='keyword' @clear='search' v-on:keyup.13='search'>
            <slot slot='prepend'></slot>
            <el-button size='small' slot='append' icon='el-icon-search' id='search-btn' @click='search'></el-button>
        </el-input>
    </div>
</template>

<script>
    export default {
        name : 'search',
        props : {
            placeholder : {
                type : String,
                default : ''
            },

            value : {
                type : [String, Number],
                default : ''
            }
        },

        data () {
            return {
                keyword : ''
            }
        },

        methods : {
            search () {
                this.$emit('input', this.keyword)
                this.$nextTick(() => {
                    this.$emit('search', this.keyword)
                })
            }
        }
    }
</script>

<style scoped lang='scss'>
    $color : #0265ff;
    #search-btn{
        background:$color;
        border-color: $color;
        color:#fff;
        border-top-left-radius: 0px;
        border-bottom-left-radius: 0px;
    }
</style>