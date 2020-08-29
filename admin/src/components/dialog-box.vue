<template>
<transition name='fade'>
  
    <div id='mask' ref='el' v-if='visible'>
        <div class='modal'>
            <span class='close' @click='confirm(false)'></span>
            <header class='header'>{{title}}</header>
            <div class='body'>
                <slot></slot>
            </div>
            <footer class='footer' v-if='footer'>
                <button class='confirm-btn' @click='confirm(true)'>确 定</button>
                <button v-if='showCancel' class='cancel-btn' @click='confirm(false)'>取 消</button>
            </footer>
        </div>
    </div>
</transition>
</template>

<script>

    export default {
        name : 'dialog-box',
        props : {
            footer : {
                type : Boolean,
                default : true
            },

            showCancel : {
                type : Boolean,
                default : true
            },
            visible : {
                type : Boolean,
                default : false
            },
            title : {
                type : String,
                default : ''
            }
        },

        data () {
            return {
                show : true
            }
        },

        methods : {
            confirm (flag) {
                if(!flag) {
                    this.$emit('update:visible', false)
                }else {
                    this.$emit('confirm')
                }
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
        z-index: 120;
        background: rgba(255, 255, 255, 0.6);

        .modal{
            position: absolute;
            left: 50%;
            top : 50%;
            transform: translate(-50%, -70%);
            background: #fff;
			border: 1px solid #d9d9d9;
			padding-bottom: 30px;
			border-radius: 10px;
            .body{
				margin-top: 20px;
				padding:0px 20px;
            }
		}

		.close{
			position: absolute;
			right: 0;
			top:0;
			height: 30px;
			width: 30px;
			background: #0ab885;
			border-radius: 0px 10px 0px 28px;
			cursor: pointer;

			&::before, &::after{
				content: '';
				position: absolute;
				width: 2px;
				height: 14px;
				background: #fff;
				left: 17px;
				top: 6px;
			}

			&::before{
				transform: rotate(-45deg);
			}
			&::after{
				transform: rotate(45deg);
			}
		}

		.header{
			line-height: 45px;
			border-bottom: 1px solid #d9d9d9;
			padding-left:30px;
		}
		
		.footer{
			text-align:center;
			margin-top: 30px;
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