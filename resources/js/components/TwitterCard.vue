<template>
    <li class="c-card p-twitter__card">
        <div class="p-twitter__profile">
            <figure>
                <img class="p-twitter__img" :src="thumbnail" alt="">
            </figure>
            <div class="p-twitter__ids">
                <p class="p-twitter__id">@{{screenName}}</p>
                <p class="p-twitter__name">{{name}}</p>
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
        },
        created() {
            this.fetchTwitterUser()
        }
    }
</script>
