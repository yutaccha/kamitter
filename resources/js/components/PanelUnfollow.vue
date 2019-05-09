<template>
    <div class="c-panel u-color__bg--white">

        <div class="p-status">
            <p class="p-status__show">{{serviceStatusLabel}}</p>
            <button class="p-status__button c-button c-button--success"
                    v-show="showRunButton"
                    @click.stop="runUnfollowService">サービス開始
            </button>
            <button class="p-status__button c-button c-button--danger"
                    v-show="showStopButton"
                    @click.stop="stopUnfollowService">停止
            </button>
        </div>
        <p>※ 自動アンフォロー機能はフォロワー5000人以内の場合、自動的に停止されます。</p>

    </div>
</template>

<script>
    import {OK} from "../utility"

    export default {
        data() {
            return {
                serviceStatus: null,
                serviceStatusLabel: null,
            }
        },
        computed: {
            showRunButton() {
                return this.serviceStatus === 1 || this.serviceStatus === 3
            },
            showStopButton() {
                return this.serviceStatus === 2 || this.serviceStatus === 3
            }
        },
        methods: {
            /**
             * APIを使用して自動アンフォローのステータスを取得する
             */
            async fetchServiceStatus() {
                const response = await axios.get('/api/system/status')
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.serviceStatus = response.data.auto_unfollow_status
                this.serviceStatusLabel = response.data.status_labels.auto_unfollow
            },

            /**
             * APIを使用して自動アンフォローを実行状態にする
             */
            async runUnfollowService() {
                const serviceType = 2
                const data = {type: serviceType}
                const response = await axios.post('/api/system/run', data)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.serviceStatus = response.data.auto_unfollow_status
                this.serviceStatusLabel = response.data.status_labels.auto_unfollow
            },

            /**
             * APIを使用して自動アンフォローを停止状態にする
             */
            async stopUnfollowService() {
                const serviceType = 2
                const data = {type: serviceType}
                const response = await axios.post('/api/system/stop', data)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.serviceStatus = response.data.auto_unfollow_status
                this.serviceStatusLabel = response.data.status_labels.auto_unfollow
            }
        },
        created() {
            this.fetchServiceStatus()
        }
    }
</script>
