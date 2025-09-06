<?php

namespace App\Repositories\Organization;

interface OrganizationRepositoryInterface
{
    /**
     * Retrieve Organization data.
     * cast is n
     * @param string $id
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */
    public function Organization($id = null);
    /**
     * Retrieve Organization Translation data.
     * cast is nt
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $locale
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */
    public function transJoin($query, $locale);
    /**
     * Retrieve Organization all Translation data.
     * cast is nt
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */
    public function transJoinLocales($query);
    /**
     * Retrieve organization Status data.
     * cast is ns
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */
    public function statusJoin($query);

    /**
     * Retrieve organization Status All Translations.
     * cast is ns
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */

    public function statusJoinAll($query);

    /**
     * Retrieve organization Status Type Translation data.
     * cast is stt
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $locale
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */
    public function statusTransJoin($query, $locale);
    /**
     * Retrieve organization TypeTrans Translation data.
     * cast is ntt
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $locale
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */
    public function typeTransJoin($query, $locale);

    /**
     * Retrieve organization Director data.
     * cast is d
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */
    public function directorJoin($query);

    /**
     * Retrieve organization Director Translation data.
     * cast is dt
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $locale
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */
    public function directorTransJoin($query, $locale);
    /**
     * Retrieve organization Email data.
     * cast is e
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */
    public function emailJoin($query);
    /**
     * Retrieve organization Contact data.
     * cast is c
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */
    public function contactJoin($query);
    /**
     * Retrieve organization Contact data.
     * cast is a
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */
    public function addressJoin($query);
    /**
     * Joins the last agreement.
     * cast is ag
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */
    public function agreementJoin($query);
    /**
     * Returns agreement documents.
     * cast is ag
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */
    public function agreementDocuments($query, $agreement_id, $locale);
    /**
     * Retrieve organization data when registered by IRD.
     * 
     *
     * @param string $organization_id
     * @param string $locale
     * @return array
     */
    public function startRegisterFormInfo($organization_id, $locale);
    /**
     * Retrieve organization data when registered by IRD.
     * 
     *
     * @param string $organization_id
     * @param string $locale
     * @return array
     */
    public function organizationProfileInfo($organization_id, $locale);
    /**
     * Retrieve organization data when registeration is completed.
     * 
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $organization_id
     * @param string $locale
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */
    public function afterRegisterFormInfo($organization_id, $locale);
    /**
     * Retrieve organization all statuses along with tanslations.
     * 
     *
     * @param string $organization_id
     * @param string $locale
     * @return \App\Repositories\organization\OrganizationRepositoryInterface|\Illuminate\Database\Query\Builder
     */
    public function statuses($organization_id, $locale);
}
