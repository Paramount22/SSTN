<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MembershipFeeRequest;
use App\Models\MembershipFee;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MembershipFeeController extends Controller
{
    public function index(): View
    {
        return view('admin.membership-fees.index', [
            'fees' => MembershipFee::orderBy('sort')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.membership-fees.create', [
            'fee' => new MembershipFee(['votes' => 1, 'sort' => (MembershipFee::max('sort') ?? 0) + 1]),
        ]);
    }

    public function store(MembershipFeeRequest $request): RedirectResponse
    {
        MembershipFee::create($request->validated());

        return redirect()->route('admin.membership-fees.index')->with('status', 'Členský príspevok bol pridaný.');
    }

    public function edit(MembershipFee $membershipFee): View
    {
        return view('admin.membership-fees.edit', ['fee' => $membershipFee]);
    }

    public function update(MembershipFeeRequest $request, MembershipFee $membershipFee): RedirectResponse
    {
        $membershipFee->update($request->validated());

        return redirect()->route('admin.membership-fees.index')->with('status', 'Členský príspevok bol uložený.');
    }

    public function destroy(MembershipFee $membershipFee): RedirectResponse
    {
        $membershipFee->delete();

        return redirect()->route('admin.membership-fees.index')->with('status', 'Členský príspevok bol zmazaný.');
    }
}
