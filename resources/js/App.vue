<template>
    <div class="l-body">
        <header class="l-header u-color__bg--white">
            <Navbar/>
        </header>
        <main class="l-main">
            <div class="container">
                <!--<Message />-->
                <transition name="page" mode="out-in">
                    <RouterView/>
                </transition>
            </div>
        </main>
        <footer class="c-foot">
            <Footer/>
        </footer>
    </div>
</template>

<script>
    import {INTERNAL_SERVER_ERROR, NOT_FOUND, UNAUTHORIED} from './utility'

    import Navbar from './components/Navbar.vue'
    import Footer from './components/Footer.vue'

    export default {
        components: {
            Navbar,
            Footer,
        },
        computed: {
            errorCode() {
                return this.$store.state.error.code
            }
        },
        watch: {
            errorCode: {
                async handler(val) {
                    if (val === INTERNAL_SERVER_ERROR) {
                        this.$router.push('/500')
                    } else if (val === UNAUTHORIED) {
                        await axios.get('/api/token/refresh')
                        this.$store.commit('auth/setUser', null)
                        this.$store.commit('auth/setTwitterUser', null)
                        this.$router.push('/login')
                    } else if (val === NOT_FOUND) {
                        this.$router.push('/notfound')
                    }
                },
                immediate: true
            },
            async $route() {
                this.$store.commit('error/setCode', null)
                await this.$store.dispatch('auth/currentUser')
                await this.$store.dispatch('auth/currentTwitterUser')
            }
        },
    }
</script>