<main class="md:container mx-4 md:mx-auto">
    <h1 class="text-2xl font-bold mb-4 select-none bg-gray-200 text-gray-800 rounded-xl px-4 py-3">
        {{lang=user_list}}
    </h1>
    <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8 mb-8">
            <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b font-medium">
                            <tr>
                                <th scope="col" class="px-6 py-4">{{lang=user_login}}</th>
                                <th scope="col" class="px-6 py-4">{{lang=user_name}}</th>
                                <th scope="col" class="px-6 py-4">{{lang=user_role}}</th>
                                <th scope="col" class="px-6 py-4">{{lang=user_status}}</th>
                                <th scope="col" class="px-6 py-4 text-center select-none">{{lang=edit}}</th>
                                <th scope="col" class="px-6 py-4 text-center select-none">{{lang=delete}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            use Tischmann\Atlantis\Template;

                            foreach ($users as $user) {
                                Template::echo('user_list_item', ['user' => $user]);
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php require 'pagination.php' ?>
    </div>
    <?php require 'user_delete_script.php' ?>
</main>