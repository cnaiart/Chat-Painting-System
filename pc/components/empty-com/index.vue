<template>
    <div class="flex flex-col items-center">
        <img class="w-[300px] h-[300px]" :src="emptyImg" />
        <div class="text-info">暂无数据～</div>
    </div>
</template>

<script setup lang="ts">
import emptyImg from '@/assets/images/empty_news.png'
</script>
