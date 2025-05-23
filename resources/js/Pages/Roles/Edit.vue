<template>
    <div>
        <h2>Edit Role</h2>
        <form @submit.prevent="submit">
            <div>
                <label for="name">Role Name:</label>
                <input type="text" id="name" v-model="form.name" placeholder="Enter role name">
            </div>
            <div>
                <button type="submit">Save</button>
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
const props = defineProps({
    role: Object,
});
const form = useForm({
    name: props.role.name
});

function submit() {
    form.put(`/roles/${props.role.id}`, {
        onFinish: () => form.reset(),
    });
}
</script>
