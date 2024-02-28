<?php $uniqid = uniqid(more_entropy: true); ?>
<div>
    <input type="radio" name="{{name}}" id="{{name}}_<?= $uniqid ?>" value="{{value}}" class="peer hidden" {{checked}} />
    <label for="{{name}}_<?= $uniqid ?>" class="block cursor-pointer select-none rounded-md p-2 text-center bg-gray-200 hover:bg-gray-300 peer-checked:bg-sky-600 peer-checked:text-white transition">{{label}}</label>
</div>