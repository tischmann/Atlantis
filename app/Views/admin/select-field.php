<div class="mb-4">
    <select <?php
            foreach ($attr ?? [] as $key => $value) {
                echo "{$key}=\"{$value}\" ";
            }

            ?> data-te-select-init name="{{name}}" id="{{id}}" aria-label="{{label}}">
        {{options}}
    </select>
    <label for="{{id}}" data-te-select-label-ref>{{label}}</label>
</div>