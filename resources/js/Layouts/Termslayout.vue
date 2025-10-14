<script setup>
    import Termstable from '@/Components/Termstable.vue'; 
    // import Sidebar from '@/Components/Sidebar.vue';
    import Navbar from '@/Components/Navbar.vue';
    import Sidebarcollege from '@/Components/Sidebarcollege.vue';
</script>

<template>
    <div>
        <Navbar />  
    </div>
 <div>
    <Sidebar/>
    <Sidebarcollege/>
 </div>

<main>
    <Termstable />
</main>
</template>