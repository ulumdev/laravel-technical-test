<script setup>
import {useForm, Link} from '@inertiajs/vue3';
const props = defineProps({
    roles: Array,
});
const form = useForm({
    name: '',
    email: '',
    password: '',
    role: '',
});
function submit() {
    form.post('/users', {
        onFinish: () => form.reset(),
    });
}
</script>

<template>
    <div>
        <h2>Create User</h2>
        <form @submit.prevent="submit">
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" v-model="form.name" placeholder="Enter name">
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" v-model="form.email" placeholder="Enter email">
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" v-model="form.password" placeholder="Enter password">
            </div>
            <div>
                <label for="role">Role:</label>
                <select v-model="form.role">
                    <option disabled value="">Select role</option>
                    <option v-for="role in roles" :value="role.name" :key="role.id">{{ role.name }}</option>
                </select>
            </div>
            <div>
                <button type="submit">Create</button>
                <Link href="/users">Cancel</Link>
            </div>
        </form>
        <div v-for="error in form.errors" :key="error">{{ error }}</div>
    </div>
</template>
