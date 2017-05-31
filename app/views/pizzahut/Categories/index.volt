

<div class="row">
    <div class="col-md-12">
        <div class="pizzabanner" style="background-image:url('{{ category['image'] }}');">
            <h1>{{ category['name'] }}</h1>
        </div>
    </div>
</div>
<h2 class="description">{{ category['description'] }}</h2>
<div class="row">
    {% for product in products %}
    <div class="col-md-6">
        <div class="product-item">
            <div class="image-container">
                <img src="{{ product['image'] }}" alt="product item" />
            </div>
            <div class="right-text">
                <h2>{{ product['name'] }}</h2>
                <p>{{ product['description'] }}</p>
                <div class="selectbox bottom-item">
                    <div class="selectdiv">
                        <select class="selectpicker">
                          <option>Fully</option>
                          <option>No Scallions</option>
                          <option>Not spicy</option>
                        </select>
                    </div>
                </div>
                <!--
                <div class="selectbox selectbox50 bottom-item">
                    <div class="selectdiv">
                        <select class="selectpicker">
                            <option>Fully</option>
                            <option>No Scallions</option>
                            <option>Not spicy</option>
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
                -->
                <div class="bottom-item">
                    {% if loop.first %}
                    <button class="button pizza-button" data-toggle="modal" data-target="#myModal">
                        <span>Order now</span>
                    </button>
                    {% else %}
                    <button class="ordernow button pizza-button" data-toggle="modal" data-target="#myModal">
                        <span>Order now</span>
                    </button>
                    {% endif %}
                </div>
            </div>
        </div>
    </div>
    {% endfor %}
</div>
