<template>
    <div>
        <h2>Roles</h2>
        <Link href="/roles/create">Add New Role</Link>
        <table>
            <thead>
                <tr>
                    <!-- <th>ID</th> -->
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="role in roles" :key="role.id">
                    <!-- <td>{{ role.id }}</td> -->
                    <td>{{ role.name }}</td>
                    <td>
                        <Link :href="`/roles/${role.id}/edit`">Edit</Link> |
                        <form :action="`/roles/${role.id}`" method="POST" @submit.prevent="deleteRole(role.id)">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import {Link, router} from '@inertiajs/vue3'
defineProps({roles: Array})
function deleteRole(id){
    if (confirm('Delete this role?')) {
        router.delete(`/roles/${id}`);
    }
}
</script>
