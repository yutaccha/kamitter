<template>
    <li class="c-card p-twitter__card" @click.prevent="setTwitterId">
        <div class="p-twitter__profile">
            <figure>
                <img class="p-twitter__img" :src="thumbnail" alt="">
            </figure>
            <div class="p-twitter__ids">
                <p class="p-twitter__name">{{name}}</p>
                <p class="p-twitter__id">@{{screenName}}</p>

            </div>
        </div>
        <div class="p-twitter__action">
            <i class="c-icon--gray p-twitter__icon fas fa-trash-alt"></i>
        </div>
    </li>
</template>

<script>
    import { OK } from '../utility'

    export default {
        props: {
            item: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                screenName: "",
                name: "",
                thumbnail: ''
            }
        },
        methods: {
            async fetchTwitterUser() {
                const response = await axios.get('/api/twitter/user/info/' + this.item.id)
                console.log(response);
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }

                this.screenName = response.data.screen_name
                this.name = response.data.name
                this.thumbnail = response.data.thumbnail
                // // console.log(users);
                // // console.log(accountNum);

            },
            async setTwitterId() {
                const response = await axios.post(`/api/twitter/${this.item.id}`)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.$router.push('/dashboard')
            }
        },
        created() {
            this.fetchTwitterUser()
        }
    }
</script>
