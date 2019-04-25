<template>
    <div class="c-panel u-color__bg--white">

        <div class="p-status">
            <p class="p-status__show">{{serviceStatus}}</p>
            <button class="p-status__button c-button c-button--success" @click.stop="runUnfollowService">サービス開始</button>
            <button class="p-status__button c-button c-button--danger" @click.stop="stopUnfollowService">停止</button>
        </div>

    </div>
</template>

<script>
    import {CREATED, OK, UNPROCESSABLE_ENTRY} from "../utility"

    export default {
        data() {
            return {
                serviceStatus: null,
            }
        },
        methods: {
            async fetchServiceStatus() {
                const response = await axios.get('/api/system/status')
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.serviceStatus = response.data.status_labels.auto_unfollow
            },
            async runUnfollowService() {
                const serviceType = 2
                const data = {type: serviceType}
                const response = await axios.post('/api/system/run', data)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.serviceStatus = response.data.status_labels.auto_unfollow
            },
            async stopUnfollowService() {
                const serviceType = 2
                const data = {type: serviceType}
                const response = await axios.post('/api/system/stop', data)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.serviceStatus = response.data.status_labels.auto_unfollow
            }
        },
        created() {
            this.fetchServiceStatus()
        }
    }
</script>
