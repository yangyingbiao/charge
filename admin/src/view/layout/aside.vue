<template>
	<aside class='x-page-aside'>
		<el-menu v-if='$store.state.asideNavMenu != null' router>
			<template v-for='(item, index) in $store.state.asideNavMenu'>
				<template v-if='item.children'>
					<template v-if='item.children.length > 1'>
						<el-submenu :key='index' :index='"index-" + index'>
							<template slot='title'>{{item.title}}</template>
							<template v-for='(v, i) in item.children'>
								<el-menu-item :route='{name : v.name}' :index='"index-" + index + "-" + i' :key='i'>{{v.title}}</el-menu-item>
							</template>
						</el-submenu>
					</template>
					<template v-else>
						<el-menu-item :route='{name : item.children[0].name}' :key='index' :index='"index-" + index'>{{item.title}}</el-menu-item>
					</template>
				</template>
				<template v-else>
					<el-menu-item :route='{name : item.name}' :key='index' :index='"index-" + index'>{{item.title}}</el-menu-item>
				</template>
			</template>
		</el-menu>
	</aside>
</template>

<script>
	export default {
		data () {
			return {
                openid : '',
                menus : []
			}
		},

		computed : {
			defaultActive () {
				return this.$route.name
			}
		}
		
	}
	
</script>

<style lang='scss'>
	.x-page-aside{
        width: 200px;
        overflow-y: auto;
        background: #fff;
        box-shadow : rgba(0, 0, 0, 0.08) 1px 1px 4px 0px;
    }
	
	.el-aside{
		overflow: visible;
	}
	.el-menu-item * {
		vertical-align : top;
	}
	.menu-icon{
		position: absolute;
		left:20px;
		top:50%;
		transform:translateY(-50%);
		width:20px;
	}
	.el-menu{
		border-right:none;
	}

	.el-menu-item {
		&.is-active {
			box-shadow: 1px 2px 12px -4px #9fa6f1;
		}
	}

	.el-menu-item, .el-submenu__title{
		position: relative;
		padding: 0px 0px 0px 60px !important;
		font-size:14px;
		line-height: 50px !important;
		height: 50px !important;
	}


	.el-submenu .el-menu-item{
		font-size: 14px;
	}


</style>