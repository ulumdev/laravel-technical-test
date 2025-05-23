<script setup>
import { Link, router } from '@inertiajs/vue3';
const props = defineProps({
    users: Array,
});
function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(`/users/${id}`);
    }
}
</script>

<template>
    <div>
        <h2>Users</h2>
        <Link href="/users/create">Add New User</Link>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role (s)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.roles.map(r => r.name).join(', ') }}</td>
                    <td>
                        <Link :href="`/users/${user.id}/edit`">Edit</Link>
                        <form :action="`/users/${user.id}`" method="POST" @submit.prevent="deleteUser(user.id)">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
