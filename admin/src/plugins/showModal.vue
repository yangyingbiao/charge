<template>
    <div id='mask' v-if='show'>
        <div class='modal'>
            <div class='body'>{{content}}</div>
            <footer class='footer'>
                <button class='confirm-btn' @click='confirm(true)'>确 定</button>
                <button class='cancel-btn' v-if='showCancel' @click='confirm(false)'>取 消</button>
            </footer>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
                show : true,
                showCancel : true,
                content : '',
                resolve : null,
                reject : null
            }
        },

        methods : {
            confirm (res) {
                 if(res) {
                     if(this.resolve) {
                         this.resolve(true)
                     }
                 }else {
                    if(this.reject) {
                         this.reject(false)
                     } 
                 }

                 this.show = false
            }
        }
    }
</script>

<style lang='scss' scoped>
    #mask{
        position: fixed;
        height: 100%;
        width: 100%;
        left :0;
        top :0;
        z-index: 100;
        background: rgba(255, 255, 255, 0.6);

        .modal{
            position: absolute;
            width : 280px;
            text-align:center;
            left: 50%;
            top : 50%;
            transform: translate(-50%, -150%);
            background: #fff;
            box-shadow: 1px 1px 10px 1px #cbc0c0;
            padding:30px 50px;
            .body{
                min-height: 60px;
                font-size: 16px;
            }
        }

        .confirm-btn, .cancel-btn{
            padding:10px 15px;
            border: none;
            background: #0ab885;
            color: #fff;
            border-radius: 4px;
        }

        .cancel-btn{
            background: #c8c8c8;
            margin-left: 30px;
        }
    }
</style>