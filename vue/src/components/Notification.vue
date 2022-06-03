<template>
    <div 
        v-if="notification.show" 
        class="fixed w-[300px] left-4 bottom-4 py-2 px-4 text-white animate-fade-in-down"
        :class="notification.type == 'success' ? 'bg-green-500' : 'bg-red-500'">
        <span>{{ notification.message }}</span>
    </div>
</template>

<script setup>
    import { computed } from 'vue';
    import { useStore } from 'vuex';

    const store = useStore();

    const notification = computed(() =>  store.state.notifications );
</script>

<style>

</style>