<template>
    <div class="h-full flex">
        <slot name="mainLeft" />
        <div class="flex-1 min-w-0 h-full px-[15px]">
            <slot />
        </div>
        <slot name="mainRight" />
    </div>
</template>
