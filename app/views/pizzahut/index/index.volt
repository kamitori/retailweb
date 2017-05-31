<!-- Wellcome -->
<div class="ng-scope">
    <div class="row welcome ng-scope">
        <div class="col-md-8">
            <h1 class="text-left ng-binding ng-scope">Hey There!</h1>
            <div>
                <p class="text-left ng-binding ng-scope">
                    Order from your local BanhMiSub, or
                    <a href="/user/signin" class="my-location ng-binding">
                    sign in
                    </a>
                    to reorder from your favorites.
                </p>
            </div>
        </div>
        <div class="col-md-4 store-button ng-scope">
            <button class="button ph-primary-button button-welcome ng-binding" data-toggle="modal" data-target="#myModal">
                Start Your Order
            </button>
        </div>
    </div>
</div>
<!-- Banner Sales -->
<div class="row banner">
    {% for image in banners %}
        <div class="col-md-{% if image.id==1 %}12{% else %}6{% endif %}">
            <a href="{{ image.link }}">
                <img src="{{ baseURL }}/{{ image.image }}" alt="banner_{{ image.id }}" />
            </a>
        </div>
        {% if image.id==1 %}
            </div><div class="row banner">
        {% endif %}
    {% endfor %}
</div>


 


<!-- Banner Sales -->
<!-- <div class="row">
    <div class="col-md-12">
        <div class="pizzabanner" style="background-image:url('{{baseURL}}/{{themeURL}}images/PH_PANormous_Feb20152.png');">
            <h1>Panormous</h1>
        </div>
    </div>
</div> -->
<!-- <h2 class="description">This Pizza is so big that it never ends.</h2>
<div class="row">
    <div class="col-md-6">
        <div class="product-item">
            <div class="image-container">
                <img src="/themes/pizzahut/images/2723e09f-0cfe-4a4b-a638-93fd5fff2864.png" alt="product item" />
            </div>
            <div class="right-text">
                <h2>Create Your Own Pizza</h2>
                <p>Create your ideal Pizza</p>
                <div class="selectbox selectbox50 bottom-item">
                    <div class="selectdiv">
                        <select class="selectpicker">
                          <option>Mustard</option>
                          <option>Ketchup</option>
                          <option>Barbecue</option>
                        </select>
                    </div>
                    <div class="selectdiv">
                    <select class="selectpicker">
                      <option>Mustard</option>
                      <option>Ketchup</option>
                      <option>Barbecue</option>
                    </select>
                    </div>
                    <div class="selectdiv">
                    <select class="selectpicker">
                      <option>Mustard</option>
                      <option>Ketchup</option>
                      <option>Barbecue</option>
                    </select>
                    </div>
                </div>
                <div class="bottom-item">
                    <form>
                        <button class="button pizza-button" type="button" data-toggle="modal" data-target="#myModal">
                        <span>Find A Store</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="product-item">
            <div class="image-container">
                <img src="/themes/pizzahut/images/89ba8837-00ed-4443-841f-9f075cdb38c5.png" alt="product item" />
            </div>
            <div class="right-text">
                <h2>Meat Lover's®</h2>
                <p>Pepperoni, Italian sausage, mild sausage, beef topping, ham, bacon crumble and pizza mozzarella.</p>
                <div class="bottom-item selectbox">
                    <div class="selectdiv">
                        <select class="selectpicker">
                          <option>Mustard</option>
                          <option>Ketchup</option>
                          <option>Barbecue</option>
                        </select>
                    </div>
                </div>
                <div class="bottom-item">
                    <form>
                        <button class="button pizza-button">
                        <span>Find A Store</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="product-item">
            <div class="image-container">
                <img src="/themes/pizzahut/images/89ba8837-00ed-4443-841f-9f075cdb38c5.png" alt="product item" />
            </div>
            <div class="right-text">
                <h2>Super Supreme</h2>
                <p>Pepperoni, Italian Sausage, mild sausage, beef topping, ham, green pepper, mushrooms, red onion, olives and pizza mozzarella.</p>
                <div class="bottom-item">
                    <form>
                        <button class="button pizza-button">
                        <span>Find A Store</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="product-item">
            <div class="image-container">
                <img src="/themes/pizzahut/images/89ba8837-00ed-4443-841f-9f075cdb38c5.png" alt="product item" />
            </div>
            <div class="right-text">
                <h2>Triple Crown®</h2>
                <p>Pepperoni, mushrooms, crisp green peppers and crowned with 100% Pizza Mozzarella.</p>
                <div class="bottom-item">
                    <form>
                        <button class="button pizza-button">
                        <span>Find A Store</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> -->

