<template>
    <div class='x-page-container'>
        <header class='x-page-header'>
            <button class='header-nav-btn' type='button' @click='toggleAsideActive'></button>
        </header>
        
        <div class='x-page-body'>
            <x-aside></x-aside>
            <div class='x-page-main'>
				<div class='x-page-scroll-container'>
					<keep-alive>
                        <router-view v-if='$route.meta.keepAlive'></router-view>
                    </keep-alive>
                    <router-view v-if='!$route.meta.keepAlive'></router-view>
				</div>
			</div>
        </div>
    </div>
</template>

<script>
import xAside from './aside.vue'
export default {
    data () {
        return {
            asideActive : false,
            menuActive : false
        }
    },

    components : {
        xAside
    },

    methods : {
        toggleAsideActive () {
            let asideActive = this.asideActive
            if(!asideActive) {
                this.asideActive = true
                setTimeout(() => {
                    this.menuActive = true
                }, 100)
            }else {
                this.menuActive = false
                setTimeout(() => {
                    this.asideActive = false
                }, 300)
            }
        },

        select (e, a) {
            this.menuActive = false
            setTimeout(() => {
                this.asideActive = false
            }, 300)
        }
    }
}
</script>

<style lang='scss' scope>
    $headerHegith : 50px;
    .x-page-container{
        height: 100%;
    }
    .x-page-header{
        height : $headerHegith;
        box-shadow : rgba(0, 0, 0, 0.08) 0px 1px 4px 0px;

        .header-nav-btn{
            height : $headerHegith;
            width : $headerHegith;
            border:none;
            border-radius:0;
            background:#409EFF;
            outline:none;
            cursor : pointer;
        }
    }

    .x-page-body{
        position: absolute;
        left: 0;
        top : $headerHegith;
        right: 0;
        bottom: 0;
        display: flex;
    }

    .x-page-main{
        flex : 1;
        height: 100%;
    }
    .x-page-scroll-container{
        padding: 30px 20px 0 20px;
        height: 100%;
        overflow-y:auto;
    }
</style>

<style>
    .el-menu{
		border-right:none;
    }
    /* .el-menu-item, .el-submenu__title{
        font-size: 14px;
    }
    .el-menu-item, .el-submenu__title, .el-submenu .el-menu-item{
        line-height: 40px;
        height: 40px;
    } */
</style>