<script setup>
import { Link, useForm } from '@inertiajs/vue3';
const props = defineProps({
    user: Object,
    roles: Array,
});
const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role: props.user.roles.length ? props.user.roles[0].name : '',
});
function submit() {
    form.put(`/users/${props.user.id}`, {
        onFinish: () => form.reset(),
    });
}
</script>

<template>
    <div>
        <h2>Edit User</h2>
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
                <label for="role">Role:</label>
                <select v-model="form.role">
                    <option disabled value="">Select role</option>
                    <option v-for="role in roles" :value="role.name" :key="role.id">{{ role.name }}</option>
                </select>
            </div>
            <div>
                <button type="submit">Save</button>
                <Link href="/users">Back</Link>
            </div>
        </form>
        <div v-for="error in form.errors" :key="error">{{ error }}</div>
    </div>
</template>
