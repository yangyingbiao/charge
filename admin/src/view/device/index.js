import { Http } from '@/utils'
	
let http = new Http()

export default {
    name : 'price',
    data () {
        return {
            filter : {
                name : ''
            }, 
            
            pageQuery : {
                name : ''
            },
            
            listData : []
        }
    },
    
    methods : {
        search : function(){
            let filter = Object.assign({}, this.filter)
            this.pageQuery = filter
            
            this.$nextTick(() => {
                this.$refs.pagination.initPage()
            })

        },
        
        load : function(rows) {
            rows.forEach(row => {
                if(row.options) {
                    if(typeof row.options === 'string') {
                        row.options = JSON.parse(row.options)
                    }
                }else {
                    row.options = []
                }

                if(row.powerClass) {
                    if(typeof row.powerClass === 'string') {
                        row.powerClass = JSON.parse(row.powerClass)
                    }
                }else {
                    row.powerClass = []
                }
            })
            
            this.listData = rows
        },
        
        async deleteDevice (id){
            let res = await this.confirm('确定删除该设备吗')
            if(res) {
                let { success } = await http.delete('device/delete', {id : id})
                if(success) {
                    this.successToast('删除成功')
                    this.$refs.pagination.initPage()
                }else {
                    this.errorToast(res.msg)
                }
            }
            
        }
            
    },
    
    activated () {
        if(!this.ISFIRST){
            this.ISFIRST = true
        }else{
            this.$refs.pagination.getPage()
        }
    }
}