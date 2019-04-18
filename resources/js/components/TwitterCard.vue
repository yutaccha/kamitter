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
            user: {
                type: Object,
                required: true
            }
        }
    }
</script>
