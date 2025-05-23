<template>
    <div>
        <h2>Create Role</h2>
        <form @submit.prevent="submit">
            <div>
                <label for="name">Role Name:</label>
                <input type="text" id="name" v-model="form.name" placeholder="Enter role name">
            </div>
            <div>
                <button type="submit">Create Role</button>
                <Link href="/roles">Cancel</Link>
            </div>
        </form>
        <div v-if="form.errors.name">
            {{ form.errors.name }}
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
const form = useForm({
    name: '',
});

function submit() {
    form.post('/roles', {
        onFinish: () => form.reset(),
    });
}
</script>
