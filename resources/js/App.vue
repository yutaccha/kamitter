<template>
    <div class="l-body">
        <header class="l-header">
            <Navbar/>
        </header>
        <main class="l-main">
            <div class="container">
                <!--<Message />-->
                <RouterView/>
            </div>
        </main>
        <footer class="c-foot">
            <Footer/>
        </footer>
    </div>
</template>

<script>
    import Navbar from './components/Navbar.vue'
    import Footer from './components/Footer.vue'

    export default {
        components: {
            Navbar,
            Footer,
        }
    }
</script>