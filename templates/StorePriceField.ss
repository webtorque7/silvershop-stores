<div class="StorePriceField">
    <% loop $Stores %>
        <div class="shop-store">
            <% if $Currencies.Count %>
            <p class="store-title">$StoreTitle</p>
            <ul class="store-currency-list">
                <% loop $Currencies %>
                    <li>
                        <label for="{$Top.Name}_{$Up.StoreID}_{$Currency}">$Currency</label>
                        <input id="{$Top.Name}_{$Up.StoreID}_{$Currency}" type="text" name="$Top.Name[$Up.StoreID][$Currency]" value="$Price">
                    </li>
                <% end_loop %>
            </ul>
            <% end_if %>
        </div>
    <% end_loop %>
</div>