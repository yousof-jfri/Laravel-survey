<template>
   <div>
        <div class="w-full px-10 py-5 bg-white shadow text-left">
            <slot name="header"></slot>
        </div>

        <!-- main content -->
        <main class="p-10 text-left">
            <slot></slot>
        </main>
   </div>
</template>

<script>
export default {
    name: 'HeaderLayout',
}
</script>

<style>

</style>