import { Component } from '@angular/core';

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.scss']
})
export class AppComponent {
    title = 'frontend';
    album?: string;

    get uploadUrl(): string {
        return `/api/albums/${this.album}/images`;
    }
}
