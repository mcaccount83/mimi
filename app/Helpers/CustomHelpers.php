<?php

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Support\Access\CategoryAccess;

// if (! function_exists('getUserAdmin')) {
//     function getUserAdmin($userAdmin)
//     {
//         return [
//             'userAdmin' => ($userAdmin == '1'),
//             'userModerator' => ($userAdmin == '2'),
//         ];
//     }
// }

// if (! function_exists('getUserType')) {
//     function getUserType($userType)
//     {
//         return [
//             'coordinator' => ($userType == 'coordinator'),  // Coordinator
//             'board' => ($userType == 'board'),  // Current Board Member
//             'outgoing' => $userType == 'outgoing',  // Outgoing Board Member
//             'disbanded' => $userType == 'disbanded',  // Disbanded Chapter Board Member
//         ];
//     }
// }

// if (! function_exists('getEOYDisplay')) {
//     function getEOYDisplay()
//     {
//         $admin = Admin::orderByDesc('id')
//             ->limit(1)
//             ->first();
//         $display_testing = ($admin->display_testing == 1);
//         $display_live = ($admin->display_live == 1);

//         // Add your new conditions
//         $displayTESTING = ($display_testing == true && $display_live != true);
//         $displayLIVE = ($display_live == true);

//         return [
//             'display_testing' => $display_testing,
//             'display_live' => $display_live,
//             'displayTESTING' => $displayTESTING,
//             'displayLIVE' => $displayLIVE,
//         ];
//     }
// }

// if (! function_exists('isActiveRoute')) {
//     function isActiveRoute(array $routes)
//     {
//         foreach ($routes as $route) {
//             if (Request::is($route)) {
//                 return 'active';
//             }
//         }

//         return '';
//     }
// }

// if (! function_exists('getUnreadForumCount')) {
//     function getUnreadForumCount()
//     {
//         $threads = Thread::recent()
//             ->with('category')
//             ->get()
//             ->filter(function ($thread) {
//                 $accessibleCategoryIds = CategoryAccess::getFilteredIdsFor(Auth::user());

//                 // If the category isn't private, allow access
//                 if (! $thread->category->is_private) {
//                     return $thread->userReadStatus != null;
//                 }

//                 // For private categories, check if user has access via CategoryAccess
//                 return $thread->userReadStatus != null &&
//                        Auth::user() &&
//                        $accessibleCategoryIds->contains($thread->category_id);
//             });

//         return $threads->whereNull('read_at')->count();
//     }
// }
