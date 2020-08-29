import axios from 'axios'
import store from '@/store'


export class Http {
    constructor(){
        this.isSendIng = false
        this.isMany = false
        this.axios = axios.create({
            baseURL : 'http://localhost:8080/admin',
            timeout : 60000,
            headers : {
                post : {
                    'Content-Type' : 'application/json'
                }
            }
        })
            
        this.source = axios.CancelToken.source()
        this.config = {}
    }

    post(url, data = {}) {
        this.config.method = 'POST'
        this.config.data = data
        return this.send(url)
    }

    put(url, data = {}) {
        this.config.method = 'PUT'
        this.config.data = data
        return this.send(url)
    }

    delete(url, data = {}) {
        this.config.method = 'DELETE'
        this.config.data = data
        return this.send(url)
    }

    get(url, params = {}) {
        this.config.method = 'GET'
        this.config.params = Object.assign({}, params)
        return this.send(url)
    }

    send(url) {
        if(!this.isSendIng) {
            this.config.headers = {}
            let token = store.state.loginToken
            if(token){
                this.config.headers.Authorization = token       
            }

            this.isSendIng = true
            this.config.url = url

            return new Promise((resolve, reject) => {
                this.axios.request(this.config).then(
                    (response) => {
                        this.isSendIng = false
                        let repdata = response.data
                        repdata.success = true
                        resolve(repdata)
                    },

                    (error) => {
                        this.isSendIng = false
                        if(error.response && error.response.data) {
                            let repdata = error.response.data
                            if(typeof repdata === 'object') { //这是应用返回的信息
                                repdata.success = false
                                resolve(repdata)
                                return
                            }
                        }
                        resolve({success : false, code : 404, msg : '请求失败', data : null})
                    }
                )
            })

        }

    }

}


export function parseTime(time, cFormat) {
	if(time == 0 || time == null){
		return ''
	}
	
  if (arguments.length === 0) {
    return null
  }
  const format = cFormat || '{y}-{m}-{d} {h}:{i}:{s}'
  let date
  if (typeof time === 'object') {
    date = time
  } else {
    if (('' + time).length === 10) time = parseInt(time) * 1000
    date = new Date(time)
  }
  const formatObj = {
    y: date.getFullYear(),
    m: date.getMonth() + 1,
    d: date.getDate(),
    h: date.getHours(),
    i: date.getMinutes(),
    s: date.getSeconds(),
    a: date.getDay()
  }
  const time_str = format.replace(/{(y|m|d|h|i|s|a)+}/g, (result, key) => {
    let value = formatObj[key]
    if (key === 'a') return ['一', '二', '三', '四', '五', '六', '日'][value - 1]
    if (result.length > 0 && value < 10) {
      value = '0' + value
    }
    return value || 0
  })
	
  return time_str
}