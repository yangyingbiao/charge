<template>
	<div style='padding-bottom:30px;' v-loading='loading'>
		<el-pagination
			background
		@size-change = 'handleSizeChange'
		@current-change = 'handleCurrentChange'
		:current-page = 'p'
		:page-sizes = '[10, 20,30,50, 100, 200]'
		:page-size = 'pageSize'
		layout = 'total, sizes, prev, pager, next, jumper'
		:total = 'total'>
		</el-pagination>
	</div>
</template>

<script>
	import { Http } from '@/utils'
	export default {
        name : 'pagination',
		props : {
			url  : {
				type : String,
				default : ''
			},
			query : {
				type : Object,
				default : function() {
					return {}
				}
			},
			size : {
				type : Number,
				default : 10
			},
			autoInit : {
				type : Boolean,
				default : true
			},
			
			showLoading : {
				type : Boolean,
				default : true
			}
		},
		
		data () {
			return {
				loading : false,
				http : new Http(),
				init : 1,
				p : 1,
				total : 0,
				pageSize : 0
			}
		},
		
		methods : {
			async getPage() {
				let query = Object.assign({}, this.query)
				query.pageNo = this.p
				query.pageSize = this.pageSize
				if(this.init == 1){
					query.init = this.init
				}
				
				if(this.showLoading) {
					this.loading = true
				}

                let res = await this.http.get(this.url, query)
                this.loading = false
                let data = res.data
                if(!data) {
                    this.init = 0
                    this.total = 0
                    this.$emit('page', [])
                }else{
                    if('totalCount' in data){
                        this.init = 0
                        this.total = data.totalCount
                    }
                    var list = data.list || []
                    list.forEach(data => {
                        data.$loading = false
                    })
                    this.$emit('page', list)
                }
			},
			
			handleSizeChange : function(size){ //每页显示多少行改变
				this.pageSize = size
				this.init = 1
				
				this.getPage(1)
			},
			
			handleCurrentChange : function(p){ //选择第几页
				this.p = p
				this.getPage(p)
			},
			
			initPage  : function(){
				this.init = 1
				this.p = 1
				this.getPage()
			}
			
		},
		
		mounted : function(){
			this.pageSize = this.size
			if(this.autoInit){
				this.initPage()
			}
		}
	}
</script>