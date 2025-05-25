<template>
    <div class="container mt-5">
        <h1>Dashboard</h1>
        <p>Welcome to the dashboard! You are logged in!</p>

        <Link href="/roles">Manage Roles</Link> |
        <Link href="/users">Manage Users (Admin only)</Link>
        <Link :href="route('logout')" method="post" as="button">Log Out</Link>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
</script>
