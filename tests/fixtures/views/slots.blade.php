{{-- Every component that takes a named slot. Slots only compile correctly in a
     real view, so they cannot be covered by Blade::render() on a string. --}}
<x-gentelella::card title="All customers">
    <x-slot:options><x-gentelella::card-options /></x-slot>
    <x-slot:footer>FOOT</x-slot>
    BODY
</x-gentelella::card>

<x-gentelella::page-header title="Tables" pretitle="Data">
    <x-slot:actions><x-gentelella::btn variant="primary">New</x-gentelella::btn></x-slot>
</x-gentelella::page-header>

<x-gentelella::stat label="Total Users" value="2,500" tone="teal" change="12%" subtext="342 new">
    <x-slot:icon><svg id="stat-icon"></svg></x-slot>
</x-gentelella::stat>

<x-gentelella::banner tone="warning">
    <x-slot:icon><svg class="banner-icon"></svg></x-slot>
    <x-slot:actions><x-gentelella::btn size="sm">Dismiss</x-gentelella::btn></x-slot>
    Heads up.
</x-gentelella::banner>

<x-gentelella::empty-state title="No items yet" description="Create your first project.">
    <x-slot:icon><svg id="empty-icon"></svg></x-slot>
    <x-gentelella::btn variant="primary" size="sm">Create</x-gentelella::btn>
</x-gentelella::empty-state>
