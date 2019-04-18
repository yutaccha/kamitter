<template>
    <li class="c-card p-twitter__card">
        <div class="p-twitter__profile">
            <figure class="p-twitter__img">
                <img src="" alt="">
            </figure>
            <div class="p-twitter__ids">
                <p class="p-twitter__id">@Front1111</p>
                <p class="p-twitter__name">ふふふろん</p>
            </div>
        </div>
        <div class="p-twitter__action">
            <i class="c-icon--gray p-twitter__icon fas fa-trash-alt"></i>
        </div>
    </li>
</template>

<script>
    export default {
        props: {
            item: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                twitterAccount: null
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

                // // console.log(users);
                // // console.log(accountNum);

            },
        },
        created() {
            this.fetchTwitterUser()
        }
    }
</script>
