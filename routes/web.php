<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return redirect('admin');
})->name('page');

Route::post('/florist', function () {
    $pk = request('pk');
    $name = request('name');
    $value = request('value');
    \Illuminate\Support\Facades\DB::table('kiotviet_invoice_details')->where('_id',$pk)->update([$name=> $value]);
})->name('page');

Route::post('/pick', function () {
    $pk = request('pk');
    $name = request('name');
    $value = request('value');
    \Illuminate\Support\Facades\DB::table('kiotviet_invoice_details')->where('_id',$pk)->update([
        'opsFlorist' => $value,
        'opsStatus' => \App\InvoiceEnum::STATUS_FLORIS_PICKED
    ]);
    return response()->json(['status' => true]);
})->name('pick');

Route::post('/assign/{id}', function ($id) {
    \Illuminate\Support\Facades\DB::table('kiotviet_invoice_details')->where('_id',$id)->update([
        'opsFlorist'=> \Admin::user()->id,
        'opsStatus' => \App\InvoiceEnum::STATUS_FLORIS_PICKED
    ]);
    return response()->json(['status' => true]);
})->name('assign');

Route::get('/index', function () {
    return view('v2.index');
})->name('page');

Route::get('/doctor-dashboard', function () {
    return view('v2.doctor-dashboard');
});
Route::get('/appointments', function () {
    return view('v2.appointments');
});
Route::get('/schedule-timings', function () {
    return view('v2.schedule-timings');
});
Route::get('/my-patients', function () {
    return view('v2.my-patients');
});
Route::get('/patient-profile', function () {
    return view('v2.patient-profile');
});
Route::get('/chat-doctor', function () {
    return view('v2.chat-doctor');
})->name('chat-doctor');
Route::get('/invoices', function () {
    return view('v2.invoices');
});
Route::get('/doctor-profile-settings', function () {
    return view('v2.doctor-profile-settings');
});
Route::get('/reviews', function () {
    return view('v2.reviews');
});
Route::get('/doctor-register', function () {
    return view('v2.doctor-register');
})->name('doctor-register');
Route::get('/map-grid', function () {
    return view('v2.map-grid');
})->name('map-grid');
Route::get('/map-list', function () {
    return view('v2.map-list');
})->name('map-list');
Route::get('/search', function () {
    return view('v2.search');
})->name('search');
Route::get('/doctor-profile', function () {
    return view('v2.doctor-profile');
})->name('doctor-profile');
Route::get('/booking', function () {
    return view('v2.booking');
})->name('booking');
Route::get('/checkout', function () {
    return view('v2.checkout');
})->name('checkout');
Route::get('/booking-success', function () {
    return view('v2.booking-success');
})->name('booking-success');
Route::get('/patient-dashboard', function () {
    return view('v2.patient-dashboard');
})->name('patient-dashboard');
Route::get('/favourites', function () {
    return view('v2.favourites');
})->name('favourites');
Route::get('/change-password', function () {
    return view('v2.change-password');
})->name('change-password');
Route::get('/profile-settings', function () {
    return view('v2.profile-settings');
})->name('profile-settings');
Route::get('/chat', function () {
    return view('v2.chat');
})->name('chat');
Route::get('/voice-call', function () {
    return view('v2.voice-call');
})->name('voice-call');
Route::get('/video-call', function () {
    return view('v2.video-call');
})->name('video-call');
Route::get('/calendar', function () {
    return view('v2.calendar');
})->name('calendar');
Route::get('/components', function () {
    return view('v2.components');
})->name('components');
Route::get('/invoice-view', function () {
    return view('v2.invoice-view');
})->name('invoice-view');
Route::get('/blank-page', function () {
    return view('v2.blank-page');
})->name('blank-page');
Route::get('/login', function () {
    return view('v2.login');
})->name('login');
Route::get('/register', function () {
    return view('v2.register');
})->name('register');
Route::get('/forgot-password', function () {
    return view('v2.forgot-password');
})->name('forgot-password');
Route::get('/blog-list', function () {
    return view('v2.blog-list');
})->name('blog-list');
Route::get('/blog-grid', function () {
    return view('v2.blog-grid');
})->name('blog-grid');
Route::get('/blog-details', function () {
    return view('v2.blog-details');
})->name('blog-details');
Route::get('/add-billing', function () {
    return view('v2.add-billing');
});
Route::get('/add-prescription', function () {
    return view('v2.add-prescription');
});
Route::get('/edit-billing', function () {
    return view('v2.edit-billing');
});
Route::get('/edit-prescription', function () {
    return view('v2.edit-prescription');
});
Route::get('/privacy-policy', function () {
    return view('v2.privacy-policy');
})->name('privacy-policy');
Route::get('/social-media', function () {
    return view('v2.social-media');
})->name('social-media');
Route::get('/term-condition', function () {
    return view('v2.term-condition');
})->name('term-condition');
Route::get('/doctor-change-password', function () {
    return view('v2.doctor-change-password');
});
/*****************ADMIN ROUTES*******************/
Route::Group(['prefix' => 'admin'], function () {
    Route::get('/index_admin', function () {
        return view('v2.admin.index_admin');
    })->name('pagee');
    Route::get('/appointment-list', function () {
        return view('v2.admin.appointment-list');
    })->name('appointment-list');
    Route::get('/specialities', function () {
        return view('v2.admin.specialities');
    })->name('specialities');
    Route::get('/doctor-list', function () {
        return view('v2.admin.doctor-list');
    })->name('doctor-list');
    Route::get('/patient-list', function () {
        return view('v2.admin.patient-list');
    })->name('patient-list');
    Route::get('/reviews', function () {
        return view('v2.admin.reviews');
    })->name('reviews');
    Route::get('/transactions-list', function () {
        return view('v2.admin.transactions-list');
    })->name('transactions-list');
    Route::get('/settings', function () {
        return view('v2.admin.settings');
    })->name('settings');
    Route::get('/invoice-report', function () {
        return view('v2.admin.invoice-report');
    })->name('invoice-report');
    Route::get('/profile', function () {
        return view('v2.admin.profile');
    })->name('profile');
    Route::get('/login', function () {
        return view('v2.admin.login');
    })->name('login');
    Route::get('/register', function () {
        return view('v2.admin.register');
    })->name('register');
    Route::get('/forgot-password', function () {
        return view('v2.admin.forgot-password');
    })->name('forgot-password');
    Route::get('/lock-screen', function () {
        return view('v2.admin.lock-screen');
    })->name('lock-screen');
    Route::get('/error-404', function () {
        return view('v2.admin.error-404');
    })->name('error-404');
    Route::get('/error-500', function () {
        return view('v2.admin.error-500');
    })->name('error-500');
    Route::get('/blank-page', function () {
        return view('v2.admin.blank-page');
    })->name('blank-page');
    Route::get('/components', function () {
        return view('v2.admin.components');
    })->name('components');
    Route::get('/form-basic-inputs', function () {
        return view('v2.admin.form-basic-inputs');
    })->name('form-basic');
    Route::get('/form-input-groups', function () {
        return view('v2.admin.form-input-groups');
    })->name('form-inputs');
    Route::get('/form-horizontal', function () {
        return view('v2.admin.form-horizontal');
    })->name('form-horizontal');
    Route::get('/form-vertical', function () {
        return view('v2.admin.form-vertical');
    })->name('form-vertical');
    Route::get('/form-mask', function () {
        return view('v2.admin.form-mask');
    })->name('form-mask');
    Route::get('/form-validation', function () {
        return view('v2.admin.form-validation');
    })->name('form-validation');
    Route::get('/tables-basic', function () {
        return view('v2.admin.tables-basic');
    })->name('tables-basic');
    Route::get('/data-tables', function () {
        return view('v2.admin.data-tables');
    })->name('data-tables');
    Route::get('/invoice', function () {
        return view('v2.invoice');
    })->name('invoice');
    Route::get('/calendar', function () {
        return view('v2.admin.calendar');
    })->name('calendar');
});






/********************ADMIN ROUTES END******************************/





