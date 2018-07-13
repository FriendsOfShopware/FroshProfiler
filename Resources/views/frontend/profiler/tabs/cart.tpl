<h2>Cart</h2>

<table>
    <thead>
    <tr>
        <th scope="col" class="key">ID</th>
        <th scope="col">Name</th>
        <th scope="col">Mode</th>
        <th scope="col">Price (Net-Price)</th>
        <th scope="col">Tax</th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
        {foreach from=$sDetail.template.vars.sBasket.content item=cartItem}
            <tr>
                <th>{$cartItem.id}</th>
                <th>{$cartItem.articlename}</th>
                <th>{$cartItem.modus}</th>
                <th>{$cartItem.amount} ({$cartItem.amountnet})</th>
                <td>{$cartItem.tax} {if !$cartItem.proportion}({$cartItem.tax_rate}%){/if}</td>
                <td>
                {if $cartItem.proportion}
                    <a class="btn btn-sm" href="#" onclick="showCartItems({$cartItem.id})">Show proportion</a>
                {/if}
                </td>
            </tr>
            {if $cartItem.proportion}
                {foreach from=$cartItem.proportion item=proportion}
                    <tr class="cart--{$cartItem.id}" style="display: none">
                        <th>{$proportion.id}</th>
                        <th>{$proportion.articlename}</th>
                        <th>{$proportion.modus}</th>
                        <th>
                            {$proportion.amount} ({$proportion.amountnet})<br>
                            <small><strong>Calucluation Help:</strong> {$cartItem.amount|replace:',':'.'} / 100 * {$sDetail.taxes[$proportion.tax_rate]->getProportionOfCart()}</small><br>
                            <small><strong>Calucluation Help Netto:</strong> Brutto / ((100 + {$proportion.tax_rate}) / 100)</small><br>
                            <small><strong>Calucluation Help Tax:</strong> Netto * ({$proportion.tax_rate} / 100)</small>
                        </th>
                        <td>{$proportion.tax}</td>
                        <td></td>
                    </tr>
                {/foreach}
            {/if}
        {/foreach}
    </tbody>
</table>

<h2>Taxes in percent</h2>

<table>
    <thead>
    <tr>
        <th scope="col" class="key">Tax</th>
        <th scope="col">Sum of Cart</th>
        <th scope="col">Proportion Of Cart</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$sDetail.taxes item=tax}
        <tr>
            <th>{$tax->getTaxRate()} %</th>
            <th>{$tax->getSumOfCart()}</th>
            <th>{$tax->getProportionOfCart()} %</th>
        </tr>
    {/foreach}
    </tbody>
</table>

<h2>Shipping Calculation</h2>

<table>
    <thead>
    <tr>
        <th scope="col" class="key">Key</th>
        <th scope="col">Value</th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <th>Shipping Net</th>
            <th>{$sDetail.template.vars.sBasket.sShippingcostsNet}</th>
        </tr>
        <tr>
            <th>Shipping With Tax</th>
            <th>{$sDetail.template.vars.sBasket.sShippingcostsWithTax}</th>
        </tr><tr>
            <th>Shipping Tax Rate</th>
            <th>{$sDetail.template.vars.sBasket.sShippingcostsTax}</th>
        </tr>
    </tbody>
</table>

{if $sDetail.template.vars.sBasket.sShippingcostsTaxProportional}
    <h2>Shipping Calculation Proportions</h2>

    <table>
        <thead>
        <tr>
            <th scope="col" class="key">Tax Rate</th>
            <th scope="col">Price</th>
            <th scope="col">Net Price</th>
            <th scope="col">Tax</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$sDetail.template.vars.sBasket.sShippingcostsTaxProportional item=tax}
            <tr>
                <th>{$tax->getTaxRate()}%</th>
                <th>{$tax->getPrice()}</th>
                <th>{$tax->getNetPrice()}</th>
                <th>{$tax->getTax()}</th>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/if}

<script>
    function showCartItems(id) {
        document.querySelectorAll('.cart--' + id).forEach(function (item) {
            item.style.display = 'table-row';
        })
    }
</script>